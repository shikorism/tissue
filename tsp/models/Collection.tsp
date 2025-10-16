import "./User.tsp";

namespace Tissue;

/** コレクション */
model Collection {
  id: int64;
  user: User;
  title: string;
  is_private: boolean = false;
  updated_at: offsetDateTime;
}
