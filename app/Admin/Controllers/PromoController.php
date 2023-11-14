<?php

namespace App\Admin\Controllers;

use App\Models\Promos;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PromoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Promos';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Promos());

        $grid->column('discount_percentage', __('Discount percentage'));
        $grid->column('title', __('Title'));
        $grid->column('description', __('Description'));
        $grid->column('valid_from', __('Valid from'));
        $grid->column('valid_to', __('Valid to'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Promos::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('discount_percentage', __('Discount percentage'));
        $show->field('title', __('Title'));
        $show->field('description', __('Description'));
        $show->field('valid_from', __('Valid from'));
        $show->field('valid_to', __('Valid to'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Promos());

        $form->number('discount_percentage', __('Discount percentage'));
        $form->text('title', __('Title'));
        $form->text('description', __('Description'));
        $form->datetime('valid_from', __('Valid from'))->default(date('Y-m-d H:i:s'));
        $form->datetime('valid_to', __('Valid to'))->default(date('Y-m-d H:i:s'));

        return $form;
    }
}
