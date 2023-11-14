<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone'));
        $grid->column('gender', __('Gender'));
        $grid->column('profile_picture', __('Profile picture'));

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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('phone', __('Phone'));
        $show->field('gender', __('Gender'));
        $show->field('about', __('About'));
        $show->field('profile_picture', __('Profile picture'));
        $show->field('role_id', __('Role id'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('last_email_send_at', __('Last email send at'));
        $show->field('password', __('Password'));
        $show->field('otp', __('Otp'));
        $show->field('otp_expired_at', __('Otp expired at'));
        $show->field('remember_token', __('Remember token'));
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
        $form = new Form(new User());

        $form->text('name', __('Name'));
        $form->email('email', __('Email'));
        $form->mobile('phone', __('Phone'));
        $form->text('gender', __('Gender'))->default('male');
        $form->text('about', __('About'));
        $form->textarea('profile_picture', __('Profile picture'));
        $form->number('role_id', __('Role id'));
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));
        $form->datetime('last_email_send_at', __('Last email send at'))->default(date('Y-m-d H:i:s'));
        $form->password('password', __('Password'));
        $form->text('otp', __('Otp'));
        $form->datetime('otp_expired_at', __('Otp expired at'))->default(date('Y-m-d H:i:s'));
        $form->text('remember_token', __('Remember token'));

        return $form;
    }
}
