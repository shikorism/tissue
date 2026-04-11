import "./CheckinSummary.tsp";

namespace Tissue;

/** ユーザーデータ */
model User {
  /** ユーザー名 */
  @maxLength(15)
  name: string;

  /** 名前 */
  @maxLength(20)
  display_name: string;

  /** チェックイン履歴の非公開フラグ */
  is_protected: boolean;

  /** いいね一覧の非公開フラグ */
  private_likes: boolean;

  profile_image_url: url;
  profile_mini_image_url: url;

  /** 自己紹介文 */
  @maxLength(160)
  bio?: string;

  /** プロフィール上に掲載するURL */
  url?: string;

  checkin_summary?: CheckinSummary;
}
