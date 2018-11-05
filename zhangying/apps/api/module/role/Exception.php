<?php
namespace api\module\role;

use ZPHP\Exception\GameException;
class Exception extends GameException
{
	const MODULE = 'Role';
	
	const SAME_NAME = 10002;							// 存在同名玩家
	const AVATAR_LIMIT = 10003;							// 头像性别
	const QUEUE_LIMIT = 10004;							// 事件队列不足
	const INVALID_LEAGUE_FACTION = 10005;				// 势力阵营不匹配
	const EXIST_LEAGUE = 10006;							// 不存在势力
	const NOT_EXIST_LEAGUE = 10007;						// 不存在联盟
	const INVALID_NAME = 10008;							// 玩家名字不合法
	const NOT_EXIST_ROLE = 10009;						// 玩家不存在
	const EXPLOIT_DOWN_LIMIT = 10010;					// exploit不足
	const NO_FAVORITES_GAME = 10011;					// 游戏未收藏
	const HAD_FAVORITES_AWARD = 10012;					// 游戏收藏奖励已领取
	const ROLE_NAME_TOO_LONG = 10013;					// 角色昵称超过12个字符
	const PARAMS_ERROR = 10014;							// 参数错误
	const NAME_ERROR = 10015;							// 含有禁用文字
}