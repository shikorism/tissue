import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/checkins")
namespace Checkins {
  op get(@path id: int64): Checkin | Error;

  op post(
    @body body: {
      checked_in_at?: offsetDateTime;
      tags?: string[];
      link?: url;
      note?: string;
      is_private?: boolean = false;
      is_too_sensitive?: boolean = false;
      discard_elapsed_time?: boolean = false;
    },
  ): Checkin | {
    @statusCode statusCode: 422;
    @body body: ValidationError;
  } | Error;

  @patch
  op patch(
    @path id: int64,
    @body body: {
      checked_in_at?: offsetDateTime;
      tags?: string[];
      link?: url;
      note?: string;
      is_private?: boolean;
      is_too_sensitive?: boolean;
      discard_elapsed_time?: boolean;
    },
  ): Checkin | {
    @statusCode statusCode: 422;
    @body body: ValidationError;
  } | Error;

  @delete
  op delete(@path id: int64): NoContentResponse | Error;
}
