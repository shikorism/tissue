import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("information")
namespace InformationApi {
  @route("latest")
  op latest(): Information[];
}
