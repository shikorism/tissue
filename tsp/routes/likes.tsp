import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/likes")
namespace Likes {
  op post(
    @body body: {
      id: int64;
    },
  ): {
    ejaculation: Checkin;
  } | Error;

  @delete
  op delete(@path id: int64): {
    ejaculation: Checkin;
  } | Error;
}
