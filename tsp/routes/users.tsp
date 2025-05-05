import "@typespec/http";
import "@typespec/openapi";
import "../models";

using TypeSpec.Http;
using TypeSpec.OpenAPI;

namespace Tissue;

@route("/users/{username}")
namespace Users {
  op get(@path username: string): User | Error;

  @route("/checkins")
  namespace Checkins {
    op list(
      @path username: string,
      @query page?: int32 = 1,

      @query
      @minValue(20)
      @maxValue(100)
      per_page?: int32 = 20,

      @query order?: "asc" | "desc" = "desc",
      @query since?: plainDate,
      @query until?: plainDate,
      @query has_link?: boolean = false,
    ): {
      @header xTotalCount: int32;
      @body body: Checkin[];
    } | Error;
  }

  @route("/collections")
  namespace Collections {
    @operationId("users-collections-list")
    op list(
      @path username: string,
      @query page?: int32 = 1,

      @query
      @minValue(20)
      @maxValue(100)
      per_page?: int32 = 20,
    ): {
      @header xTotalCount: int32;
      @body body: Collection[];
    } | Error;
  }

  @route("/stats")
  namespace Stats {
    @route("/checkin")
    namespace Checkin {
      @route("/daily")
      op daily(
        @path username: string,
        @query since?: plainDate,
        @query until?: plainDate,
      ): DailyCheckinSummary[] | Error;

      @route("/hourly")
      op hourly(
        @path username: string,
        @query since?: plainDate,
        @query until?: plainDate,
      ): HourlyCheckinSummary[] | Error;
    }

    @route("/links")
    op links(@path username: string, @query since?: plainDate, @query until?: plainDate): MostlyUsedLink[] | Error;

    @route("/tags")
    op tags(@path username: string, @query since?: plainDate, @query until?: plainDate): MostlyUsedCheckinTag[] | Error;
  }
}
