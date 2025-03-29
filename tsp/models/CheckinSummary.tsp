namespace Tissue;

/** チェックインの概況 */
model CheckinSummary {
  /** 最後のチェックインからの経過秒数 (現在のセッション) */
  current_session_elapsed: int64;

  /** 合計チェックイン回数 */
  total_checkins: int64;

  /** 合計時間 (秒) */
  total_times: int64;

  /** チェックイン間隔の平均値 (秒) */
  average_interval: int64;

  /** チェックイン間隔の中央値 (秒) */
  median_interval: int64;

  /** チェックイン間隔の最大値 (秒) */
  longest_interval: int64;

  /** チェックイン間隔の最小値 (秒) */
  shortest_interval: int64;
}
