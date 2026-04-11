namespace Tissue;

model CollectionItem {
  id: int64;
  collection: Collection;
  link: url;
  note: string;
  tags: string[];
  #deprecated "Do not use in private API; use `collection.id` instead"
  collection_id: int64;
}
