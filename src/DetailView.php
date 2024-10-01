<?php

namespace samuelelonghin\detailview;

use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use samuelelonghin\db\UrlTrait;
use samuelelonghin\form\BaseActiveForm;
use Yii;
use yii\helpers\ArrayHelper;

class DetailView extends \kartik\detail\DetailView implements BootstrapInterface
{
    use BootstrapTrait;

    const INPUT_ROUND_CHECKBOX = 'round-checkbox';
    const INPUT_LAT_LON = 'latLonInput';
    const FORMAT_NUMBER = 'number';
    const INPUT_NUMBER = 'numberInput';
    const FORMAT_DATE = 'date';
    const INPUT_DATE = 'dateInput';
    const FORMAT_BOOLEAN = 'boolean';
    const INPUT_SWITCH = 'switchInput';
    const FORMAT_TEXT_AREA = 'textArea';

    public $formAction;

    public $formClass = BaseActiveForm::class;
    public $userPermissionUpdate;
    public $userPermissionDelete;
    public $userClass;

    public $panelTemplate = <<< HTML
{panelHeading}
{items}
{panelAfter}
{panelFooter}
HTML;

    public $panelCssPrefix = 'll';
    public $labelColOptions = ['style' => 'width: 10%'];
    public $bordered = false;
    public $striped = false;
    public $condensed = false;
    public $responsive = true;
    public $hover = false;
    public $hAlign = null;
    public $vAlign = null;
    public $viewOptions = ['class' => 'btn btn-link border-0'];
    public $saveOptions = ['class' => 'btn btn-primary border-0'];
    public $updateOptions = ['class' => 'btn btn-link border-0'];
    public $resetOptions = ['class' => 'btn btn-outline-primary'];
    public $panel = [
        'type' => DetailView::BS_PANEL,
    ];
    public $enableEditMode = null;

    /**
     * @var UrlTrait
     */
    public $model;
    public $canDelete = null;
    public $canUpdate = null;

    public function init()
    {
        self::$_inputsList = ArrayHelper::merge(self::$_inputsList, [self::INPUT_NUMBER => 'numberInput', self::INPUT_LAT_LON => 'latLonInput', self::INPUT_DATE => 'dateInput', self::INPUT_SWITCH => 'switchInput']);

        if (array_key_exists('userPermissionDelete', Yii::$app->params)) $this->userPermissionDelete = Yii::$app->params['userPermissionDelete'];
        if (array_key_exists('userPermissionUpdate', Yii::$app->params)) $this->userPermissionUpdate = Yii::$app->params['userPermissionUpdate'];
        if (array_key_exists('userClass', Yii::$app->params)) $this->userClass = Yii::$app->params['userClass'];

        DetailViewAsset::register($this->getView());
        parent::init();
    }

    protected function initWidget()
    {
        self::$_inputsList = ArrayHelper::merge(parent::$_inputsList, [self::INPUT_ROUND_CHECKBOX => 'round-checkbox']);
        if (!$this->deleteOptions)
            $this->deleteOptions = [
                'url' => $this->model::getUrlTo(['delete', 'id' => $this->model->id]),
                'confirm' => Yii::t('app', 'Sei sicuro di voler eliminare?'),
                'class' => 'btn btn-outline-danger'
            ];
        if (!$this->formOptions)
            $this->formOptions = [
                'id' => 'classe-form-' . $this->model->id,
                'enableAjaxValidation' => true,
            ];
        if (!$this->formAction)
            $this->formAction = $this->model::getUrlTo(['update', 'id' => $this->model->id]);

        if ($this->canDelete === null && isset($this->userClass) && isset($this->userPermissionDelete))
            $this->canDelete = $this->userClass::_can(get_class($this->model), $this->model->id, $this->userPermissionDelete);
        if ($this->canUpdate === null && isset($this->userClass) && isset($this->userPermissionDelete))
            $this->canUpdate = $this->userClass::_can(get_class($this->model), $this->model->id, $this->userPermissionUpdate);


        if ($this->enableEditMode === null && ($this->canUpdate !== false || $this->canDelete !== false))
            $this->enableEditMode = true;
        if ($this->canDelete === false)
            $this->buttons1 = str_replace('{delete}', '', $this->buttons1);
        if ($this->canUpdate === false)
            $this->buttons1 = str_replace('{update}', '', $this->buttons1);

        parent::initWidget();
        if ($this->_form)
            $this->_form->action = $this->formAction;
    }

    protected function renderFormAttribute($config)
    {
        if (isset($config['format']) && !isset($config['type'])) {
            switch ($config['format']) {
                case self::FORMAT_NUMBER:
                    $config['type'] = self::INPUT_NUMBER;
                    break;
                case self::FORMAT_DATE:
                    $config['type'] = self::INPUT_DATE;
                    break;
                case self::FORMAT_BOOLEAN:
                    $config['type'] = self::INPUT_SWITCH;
                    break;
                case self::FORMAT_TEXT_AREA:
                    $config['type'] = self::INPUT_TEXTAREA;
                    $config['options'] = ['rows' => (array_key_exists('value', $config) && $config['value'] != null ? substr_count($config['value'], "\n") : 0) + 5,];
                    break;
            }
        }
        return parent::renderFormAttribute($config);
    }

    protected function parseAttributeItem($attribute): array|string
    {
        if (!is_string($attribute) && !isset($attribute['attribute']) && isset($attribute[0]))
            $attribute['attribute'] = array_shift($attribute);
        return parent::parseAttributeItem($attribute);
    }
}
