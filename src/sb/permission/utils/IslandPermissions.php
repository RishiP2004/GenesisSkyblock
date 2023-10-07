<?php

namespace sb\permission\utils;

class IslandPermissions {

    public const PERMISSION_BUILD = "build";

    public const PERMISSION_BREAK = "break";

    public const PERMISSION_INVITE = "invite";

    public const PERMISSION_KICK = "kick";

	public const PERMISSION_DAMAGE = "damage";

    public const PERMISSION_OPEN = "open";

    public const PERMISSION_ALL = "all";

    public const PERMISSION_LOCK = "lock";

	public const PERMISSION_EDIT_ROLE = "edit_role";

	public const PERMISSION_SET_ROLE = "set_role";

	const ALL_PERMISSIONS = [
		self::PERMISSION_BUILD,
		self::PERMISSION_BREAK,
		self::PERMISSION_INVITE,
		self::PERMISSION_KICK,
		self::PERMISSION_DAMAGE,
		self::PERMISSION_OPEN,
		self::PERMISSION_LOCK,
		self::PERMISSION_EDIT_ROLE,
		self::PERMISSION_SET_ROLE
	];
}