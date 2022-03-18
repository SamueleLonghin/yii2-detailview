<?php

/**
 * @package   yii2-detailview
 * @author    Samuele Longhin
 */

namespace samuelelonghin\detailview;

use yii\web\AssetBundle;

/**
 * Asset bundle for DetailView Widget
 *
 */
class DetailViewAsset extends AssetBundle
{
	public $depends = [
		"rmrevin\yii\fontawesome\AssetBundle"
	];
}