<?php

namespace App\Admin\Controllers;

use App\Models\MaidSchedule;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class MaidScheduleController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'MaidSchedule';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new MaidSchedule());
        $grid->disableCreateButton();

        $grid->column('user.name', __('User'));
        $grid->column('maid.name', __('Maid'));
        $grid->column('start_date', __('Start date'));
        $grid->column('end_date', __('End date'));
        $grid->column('duration_value', __('Duration'));
        $grid->column('duration_unit', __('Unit'));

        $grid->actions(function ($actions) {
            $actions->disableDelete();
            $actions->disableEdit();
        });

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
        $show = new Show(MaidSchedule::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('maid_id', __('Maid id'));
        $show->field('start_date', __('Start date'));
        $show->field('end_date', __('End date'));
        $show->field('duration_value', __('Duration value'));
        $show->field('duration_unit', __('Duration unit'));
        $show->field('session', __('Session'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new MaidSchedule());

        $form->number('user_id', __('User id'));
        $form->number('maid_id', __('Maid id'));
        $form->date('start_date', __('Start date'))->default(date('Y-m-d'));
        $form->date('end_date', __('End date'))->default(date('Y-m-d'));
        $form->number('duration_value', __('Duration value'));
        $form->text('duration_unit', __('Duration unit'));
        $form->number('session', __('Session'));

        return $form;
    }
}
