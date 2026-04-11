import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/stats")
namespace Stats {
  @route("/checkin")
  namespace Checkin {
    @route("/daily")
    op daily(): DailyCheckinSummary[] | Error;
  }
}
