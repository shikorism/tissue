import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/timelines")
namespace Timelines {
  @route("/public")
  op `public`(
    @query page?: int32 = 1,

    @query
    @minValue(20)
    @maxValue(100)
    per_page?: int32 = 20,
  ): {
    @header xTotalCount: int32;
    @body body: Checkin[];
  };
}
