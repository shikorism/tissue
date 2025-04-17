import "./User.tsp";

namespace Tissue;

/** チェックインデータ */
model Checkin {
  id: int64;
  checked_in_at: offsetDateTime;
  tags: string[];
  @maxLength(2000) link: url;
  @maxLength(500) note: string;
  is_private: boolean = false;
  is_too_sensitive: boolean = false;
  discard_elapsed_time: boolean = false;
  source: "web" | "csv" | "webhook" | "api";
  user: User;
  likes_count?: integer;
  is_liked?: boolean;
}
