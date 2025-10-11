import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/tags")
namespace Tags {
  op list(@query page?: int32 = 1): {
    @header xTotalCount: int32;
    @body body: Tag[];
  };
}

@route("/recent-tags")
op recentTags(): string[];
