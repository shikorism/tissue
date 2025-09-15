import "@typespec/http";
import "../models";

using TypeSpec.Http;

namespace Tissue;

@route("/collections")
namespace Collections {
  op list(): Collection[];

  op get(@path collection_id: int64): Collection | Error;

  op post(
    @body body: {
      title: string;
      is_private: boolean = false;

      @maxItems(100)
      items?: {
        link: url;
        note?: string;
        @maxItems(40) tags?: string[];
      }[];
    },
  ): Collection | {
    @statusCode statusCode: 422;
    @body body: ValidationError;
  } | Error;

  @put
  op put(
    @path collection_id: int64,
    @body body: {
      title: string;
      is_private: boolean = false;
    },
  ): Collection | {
    @statusCode statusCode: 422;
    @body body: ValidationError;
  } | Error;

  @delete
  op delete(@path collection_id: int64): Collection | Error;

  @route("/{collection_id}/items")
  namespace CollectionItems {
    op list(
      @path collection_id: int64,
      @query page?: int32 = 1,

      @query
      @minValue(20)
      @maxValue(100)
      per_page?: int32 = 20,
    ): {
      @header xTotalCount: int32;
      @body body: CollectionItem[];
    } | Error;

    op post(
      @path collection_id: int64,
      @body body: {
        link: url;
        note?: string;
        @maxItems(40) tags?: string[];
      },
    ): CollectionItem | {
      @statusCode statusCode: 422;
      @body body: ValidationError;
    } | Error;

    @patch
    op patch(
      @path collection_id: int64,
      @path collection_item_id: int64,
      @body body: {
        note?: string;
        @maxItems(40) tags?: string[];
      },
    ): CollectionItem | {
      @statusCode statusCode: 422;
      @body body: ValidationError;
    } | Error;

    @delete
    op delete(@path collection_id: int64, @path collection_item_id: int64): NoContentResponse | Error;
  }
}
