<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\RichMenu
 *
 * @property int $id
 * @property int $rich_menu_type
 * @property int $x
 * @property int $y
 * @property int $width
 * @property int $height
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereRichMenuType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereWidth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\RichMenu whereY($value)
 * @mixin \Eloquent
 */
class RichMenu extends Model
{
    protected $table  = "rich_menus";

    // * Image size (pixels): 2500x1686, 2500x843, 1200x810, 1200x405, 800x540, 800x270
    public const WIDTH = 2500;
    public const HEIGHT = 1686;
    // 赤
    // http://image.intervention.io/getting_started/formats
    public const COLOR = '#ff0000';
}
