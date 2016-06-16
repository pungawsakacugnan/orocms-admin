<?php
return [
    /**
     * Defaults
     */
    'defaults.list.status.published' => 'Active',
    'defaults.list.status.unpublished' => 'Disabled',
    'defaults.list.status.deleted' => 'Deleted',

    /**
     * Menu
     */
    'menu.dashboard' => 'Dashboard',
    'menu.modules' => 'Modules',
    'menu.modules.listing' => 'View All Modules',
    'menu.plugins' => 'Plugins',
    'menu.accounts' => 'Manage Accounts',
    'menu.accounts.listing' => 'View Listing',
    'menu.accounts.add' => 'Add an Account',
    'menu.settings' => 'Settings',
    'menu.logs' => 'Logs',


    /**
     * Dashboard
     */
    'dashboard.header' => 'Dashboard',
    'dashboard.breadcrumb' => 'Dashboard',

    /**
     * Modules
     */
    'module.header' => 'Manage Modules',
    'module.breadcrumb' => 'Manage Modules',

    'module.list.header.name' => 'Module Name',
    'module.list.header.path' => 'Module Path',
    'module.list.header.status' => 'Status',
    'module.list.button.delete_all' => 'Remove selected modules',

    'module.message.enable.success' => 'Module ":module" successfully enabled.',
    'module.message.disable.success' => 'Module ":module" successfully disabled.',

    /**
     * Plugins
     */
    'plugin.header' => 'Manage Plugins',
    'plugin.breadcrumb' => 'Manage Plugins',

    'plugin.list.header.name' => 'Plugin Name',
    'plugin.list.header.path' => 'Plugin Path',
    'plugin.list.header.status' => 'Status',
    'plugin.list.button.delete_all' => 'Remove selected plugins',

    'plugin.message.enable.success' => 'Plugin ":plugin" successfully enabled.',
    'plugin.message.disable.success' => 'Plugin ":plugin" successfully disabled.',

    /**
     * Users
     */
    'user.header' => 'Manage Users',
    'user.breadcrumb' => 'Manage Users',

    'user.list.header.name' => 'Name',
    'user.list.header.email' => 'Email Address',
    'user.list.header.role' => 'Role',
    'user.list.header.date_added' => 'Date Added',
    'user.list.header.published' => 'Status',
    'user.list.header.id' => 'ID',
    'user.list.button.create' => 'New Account',
    'user.list.button.delete_all' => 'Remove selected accounts',
    'user.list.button.purge' => 'Delete Forever',
    'user.list.button.restore' => 'Restore...',

    'user.list.dropdown.visibility.all' => 'Display All',
    'user.list.dropdown.visibility.active' => 'Active',
    'user.list.dropdown.visibility.disabled' => 'Disabled',
    'user.list.dropdown.visibility.deleted' => 'Deleted',

    'user.form.create.header' => 'Create User',
    'user.form.edit.header' => 'Edit User',
    'user.form.button.save' => 'Save Details',
    'user.form.button.update' => 'Update Details',
    'user.form.button.restore' => 'Restore',
    'user.form.button.cancel' => 'or Cancel',
    'user.form.button.close' => 'or Close',

    'user.form.label.name' => 'Name',
    'user.form.label.email' => 'Email Address',
    'user.form.label.password' => 'Password',
    'user.form.label.role' => 'Role',
    'user.form.label.published' => 'Status',

    'user.message.create.success' => 'User details successfully created.',
    'user.message.update.success' => 'User details successfully updated.',
    'user.message.delete.marked' => 'User item marked as deleted.',
    'user.message.delete.success' => 'User details permanently deleted.',
    'user.message.restored' => 'User details successfully restored',

    /**
     * Logs
     */
    'logs.header' => 'Logs',
    'logs.breadcrumb' => 'Manage Logs',

    'logs.list.header.date' => 'Date',
    'logs.list.header.level' => 'Log Level',
    'logs.list.header.message' => 'Description',

    /**
     * Profile
     */
    'profile.header' => 'Edit Profile',
    'profile.logout' => 'Log Out',
    'profile.edit' => 'Edit Profile',

    'profile.form.label.name' => 'Name',
    'profile.form.label.email' => 'Email Address',
    'profile.form.label.password.description' => 'Leave password fields blank if you don\'t want change it.',
    'profile.form.label.password' => 'Password',
    'profile.form.label.confirm_password' => 'Confirm Password',

    'profile.form.button.update' => 'Update Profile Details',
    'profile.form.button.cancel' => 'or Cancel',

    'profile.error.header' => 'Something went wrong!',
    'profile.message.updated' => 'Profile successfully updated.',
];