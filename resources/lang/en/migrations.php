<?php
/**
 * Initial database seed data
 */
return [

	'users' => [
		'admin' => 'Admin',
		'guest' => 'Guest',
	],

	'roles' => [

		'admin' => [
			'display_name' => 'Admin',
			'description' => 'Administrator of the whole application',
		],
		'editor' => [
			'display_name' => 'Editor',
			'description' => 'User can edit Books, Chapters & Pages',
		],
		'viewer' => [
			'display_name' => 'Viewer',
			'description' => 'User can view books & their content behind authentication',
		],
		'public' => [
			'display_name' => 'Public',
			'description' => 'The role given to public visitors if allowed',
		],
	],

	'permissions' => [
		'settings-manage' => 'Manage Settings',
		'users-manage' => 'Manage Users',
		'user-roles-manage' => 'Manage Roles & Permissions',
		'restrictions-manage-all' => 'Manage All Entity Permissions',
		'restrictions-manage-own' => 'Manage Entity Permissions On Own Content',
		'entities' => [
			'settings' => 'Settings', // legacy
			'user' => 'User', // legacy
			'book' => 'Books',
			'page' => 'Pages',
			'chapter' => 'Chapters',
			'image' => 'Images',
			'bookshelf' => 'BookShelves',
		],
		'ops' => [
			'create' => 'Create', // legacy
			'create-all' => 'Create All',
			'create-own' => 'Create Own',
			'update' => 'Update', // legacy
			'update-all' => 'Update All',
			'update-own' => 'Update Own',
			'delete' => 'Delete', // legacy
			'delete-all' => 'Delete All',
			'delete-own' => 'Delete Own',
			'view-all' => 'View All',
			'view-own' => 'View Own',
		]
	],

];
