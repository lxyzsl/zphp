<?php
namespace api\module\role;

class Struct
{
	public $RoleID = 0;							// 标识列
	public $Name = '';							// 玩家名字
	public $Sex = '';							// 玩家性别
	public $Level = 1;							// 玩家等级
	public $Exp = 0;							// 玩家经验
	public $FactionID = 0;						// 玩家阵形
	public $LeagueID = 0;						// 联盟标示
// 	public $BuildingQueue = 0;					// 建筑队列数量
	public $SummonQueue = 0;					// 召唤队列数量
	public $AvatarID = 0;						// 头像标识
	public $UserID = 0;							// 账户标识
	public $SkillPoints = 1;					// 技能点
	public $Switchover = 'One';					// 技能树开关
	public $SkillOne = 0;						// 技能树1所用点数
	public $SkillTwo = 0;						// 技能树2所用点数
	public $CreateDate = 0;						// 创建时间
	public $Opening = '';						// 开场动画播放记录
	public $Exploit = 0;						// 功勋值
	public $Honor = 0;							// 军衔值
	public $MilitarLevel = 1;					// 军衔等级
	public $MonthExpireTime = 0;				// 月卡过期时间
	public $GrowthFund = 0;						// 成长基金
	public $Novice = 1;							// 是否在新手保护内
	public $Favorites = 0;                      // 游戏收藏状态标识 0：未收藏，1：已收藏未领取奖励，2：已收藏并领取奖励
	public $TrialsBuff = 0;						// 试炼buff记录
	public $AddLeagueTime = 0;					// 加入联盟的时间
	public $RefreshDetectionTime = 0;			// 调查点下次刷新时间戳
	public $HonorExploitParams = '';			// 每天[劫掠,或者其他]对荣誉和军衔上限要求
	public $IsTechUpgrade = 0;					// 是否有科技升级
	public $IsScan = 1;							// 是否探测过
	public $ExploitTotal = 0;					// 累计消耗功勋值
	public $DataVer = '';
}