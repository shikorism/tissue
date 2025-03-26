namespace Tissue;

model CollectionItem {
  id: int64;
  collection_id: int64;
  link: url;
  note: string;
  tags: string[];
}
