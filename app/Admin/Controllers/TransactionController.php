<?php

namespace App\Admin\Controllers;

use App\Models\Transactions;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class TransactionController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Transactions';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Transactions());
        $grid->disableCreateButton();

        $grid->column('user.name', __('User'));
        $grid->column('maid.name', __('Maid'));
        $grid->column('order_id', __('Transaction ID'));
        $grid->column('hourly_price', __('Hourly price'));
        $grid->column('amount', __('Amount'));
        $grid->column('discount_amount', __('Discount amount'));
        $grid->column('total_amount', __('Total amount'));
        $grid->column('status', __('Status'));

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
        $show = new Show(Transactions::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('user_id', __('User id'));
        $show->field('maid_id', __('Maid id'));
        $show->field('order_id', __('Order id'));
        $show->field('schedule_id', __('Schedule id'));
        $show->field('hourly_price', __('Hourly price'));
        $show->field('amount', __('Amount'));
        $show->field('discount_amount', __('Discount amount'));
        $show->field('total_amount', __('Total amount'));
        $show->field('status', __('Status'));
        $show->field('payment_response', __('Payment response'));
        $show->field('payment_link', __('Payment link'));
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
        $form = new Form(new Transactions());

        $form->number('user_id', __('User id'));
        $form->number('maid_id', __('Maid id'));
        $form->text('order_id', __('Order id'));
        $form->number('schedule_id', __('Schedule id'));
        $form->decimal('hourly_price', __('Hourly price'));
        $form->decimal('amount', __('Amount'));
        $form->decimal('discount_amount', __('Discount amount'));
        $form->decimal('total_amount', __('Total amount'));
        $form->text('status', __('Status'));
        $form->textarea('payment_response', __('Payment response'));
        $form->textarea('payment_link', __('Payment link'));

        return $form;
    }
}
