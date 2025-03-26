namespace Tissue;

model MostlyUsedCheckinTag {
  @maxLength(255)
  name: string;

  count: int32;
}
