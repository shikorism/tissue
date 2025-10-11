namespace Tissue;

model Information {
  id: int64;
  category: "news" | "update" | "bug" | "maintenance";
  pinned: boolean;
  title: string;
  created_at: offsetDateTime;
}
