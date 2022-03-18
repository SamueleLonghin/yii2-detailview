<?php

namespace samuelelonghin\detailview;

use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use samuelelonghin\form\BaseActiveForm;
use Yii;
use yii\helpers\ArrayHelper;

class DetailView extends \kartik\detail\DetailView implements BootstrapInterface
{
	use BootstrapTrait;

	const INPUT_ROUND_CHECKBOX = 'round-checkbox';
	const INPUT_LAT_LON = 'latLonInput';
	const INPUT_NUMBER = 'numberInput';
	const FORMAT_NUMBER = 'number';

	public $formAction;

	public $formClass = BaseActiveForm::class;
	public $userPermissionUpdate;
	public $userPermissionDelete;
	public $userClass;//    public $formClass = AsyncForm::class;

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
	public $canDelete = null;
	public $canUpdate = null;

	public function init()
	{
		self::$_inputsList = ArrayHelper::merge(self::$_inputsList, [self::INPUT_NUMBER => 'numberInput', self::INPUT_LAT_LON => 'latLonInput']);

		parent::init();
	}

	protected function initWidget()
	{
		self::$_inputsList = ArrayHelper::merge(parent::$_inputsList, [self::INPUT_ROUND_CHECKBOX => 'round-checkbox']);
		if (!$this->deleteOptions)
			$this->deleteOptions = [
				'url' => [$this->model::getController() . '/delete', 'id' => $this->model->id],
				'confirm' => Yii::t('app', 'Sei sicuro di voler eliminare?'),
				'class' => 'btn btn-outline-danger'
			];
		if (!$this->formOptions)
			$this->formOptions = [
				'id' => 'classe-form-' . $this->model->id,
				'enableAjaxValidation' => true,
			];
		if (!$this->formAction)
			$this->formAction = [$this->model::getController() . '/update', 'id' => $this->model->id];

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
					$config['type'] = DetailView::INPUT_NUMBER;
			}
		}
		return parent::renderFormAttribute($config);
	}

	protected function parseAttributeItem($attribute)
	{
		if (!is_string($attribute) && !isset($attribute['attribute']) && isset($attribute[0]))
			$attribute['attribute'] = array_shift($attribute);
		return parent::parseAttributeItem($attribute);

	}
}

