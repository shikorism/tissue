import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/recent-tags")
op recentTags(): string[];
