namespace Tissue;

model HourlyCheckinSummary {
  @minValue(0)
  @maxValue(32)
  hour: int32;

  count: int32;
}
