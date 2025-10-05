import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/search")
namespace Search {
  @route("/checkins")
  op checkins(
    @query q: string,
    @query page?: int32 = 1,

    @query
    @minValue(20)
    @maxValue(100)
    per_page?: int32 = 20,
  ): {
    @header xTotalCount: int32;
    @body body: Checkin[];
  };

  @route("/collections")
  op collections(
    @query q: string,
    @query page?: int32 = 1,

    @query
    @minValue(20)
    @maxValue(100)
    per_page?: int32 = 20,
  ): {
    @header xTotalCount: int32;
    @body body: (CollectionItem & {
      collection: Collection;
    })[];
  };

  @route("/tags")
  op tags(
    @query q: string,
    @query page?: int32 = 1,

    @query
    @minValue(20)
    @maxValue(100)
    per_page?: int32 = 50,
  ): {
    @header xTotalCount: int32;
    @body body: {
      name: string;
    }[];
  };
}
