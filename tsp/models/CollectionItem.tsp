namespace Tissue;

model CollectionItem {
  id: int64;
  collection_id: int64;
  link: url;
  note: string;
  tags: string[];

  // FIXME: 後で廃止したい
  user_name: string;
  // TODO: ここに含められるよう他の場所の定義を修正したい
  //collection: Collection;
}
