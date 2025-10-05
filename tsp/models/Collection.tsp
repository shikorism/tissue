import "./User.tsp";

namespace Tissue;

/** コレクション */
model Collection {
  id: int64;
  user_id: int64;
  user_name: string;
  title: string;
  is_private: boolean = false;
  updated_at: offsetDateTime;
  user: User;
}
