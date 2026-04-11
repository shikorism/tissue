import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/me")
op me(): User;
