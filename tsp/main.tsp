import "@typespec/http";
import "./models";
import "./routes";

using TypeSpec.Http;

@service(#{ title: "Tissue internal API" })
namespace Tissue;
