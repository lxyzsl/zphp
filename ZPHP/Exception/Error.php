<?php
namespace ZPHP\Exception;

final class Error
{
	const MODULE = 'Error';
	
    /**
     * Code List
     */
	const DB_NEED_TRANSACTION = 10001;				// MySQL未开启事务处理
	const DB_FIELD_NOT_FOUND = 10002;				// MySQL字段不存在
	const DB_NONE_FORUPDATE = 10003;				// 未开启forUpdate
	const DB_CONNECTION_FAILED = 10004;				// 数据库连接失败
    
	const COMMON_PARAM_ERROR = 11001;				// 参数错误
	const COMMON_LACK_PARAM = 11002;				// 参数不足
	const COMMON_ACCESS_OTHERS_DATA = 11003;		// 不可操作他人数据
	const GAME_MODULE_CLOSE = 11004;				// 游戏模块关闭
	const COMMON_ROLE_PARAM = 11005;				// 需玩家角色信息
    
	const OBJ_ACCESS_NULL = 12001;					// 无权限操作对象
	const OBJ_NONE_INITIALIZED = 12002;				// 对象未初始化
	const DB_FATAL_ERROR = 908;						// 死锁
	
	const NOT_EXITS_USER = 402;						// 充值是传递的UserID不存在
	const SAME_ORDER = 999;							// 相同的订单号（充值）
	
	const INVALID_SERVER_ID = 14001;				// 无效的服务器选择
	const INVALID_SERVICE = 14002;					// 无效的服务入口
	const INVALID_FUNC = 14003;						// 无效的接口名
	
	// zphp
	const NO_CLASS = 13001;							// noclass%s
	const NO_CONFIG = 13002;						// noconfig%s
	
	
}