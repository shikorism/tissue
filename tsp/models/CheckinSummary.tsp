namespace Tissue;

/** チェックインの概況 */
model CheckinSummary {
  /** 最後のチェックインからの経過秒数 (現在のセッション) */
  current_session_elapsed: int64;

  /** 合計チェックイン回数 */
  total_checkins: int64;

  /** 合計時間 (秒) */
  total_times: int64;

  /** 平均チェックイン間隔 (秒) */
  average_interval: int64;

  /** 最長チェックイン間隔 (秒) */
  longest_interval: int64;

  /** 最短チェックイン間隔 (秒) */
  shortest_interval: int64;
}
