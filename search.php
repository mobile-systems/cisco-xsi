<?
{
          (rateLimit(ip) & alwaysCache(cache)) {
            parameters('q ?, 'tag ?, 'start.as[Int] ? 0) { (q, tag, start) =>
              validate(q.isDefined || tag.isDefined, errorMsg = "need a query or a tag to search (q/tag params)")
              validate(start >= 0, errorMsg = s"start parameter must be positive: $start")

              respondWithHeader(RawHeader("Refresh", s"0; url=http://$hostname/search.xml?q=${q.orElse(tag).get}&start=${start + 25}")) {
                ctx =>
                  val uri = q match {
                    case Some(query) =>
                      lastSearches += (ip -> (lastSearches.getOrElse(ip, new DistinctEvictingList[String](10)) += query))
                      capsuleUri.copy(query = Query("q" -> query, "start" -> start.toString, "limit" -> "25"))
                    case None => capsuleUri.copy(query = Query("tag" -> tag.getOrElse("")))
                  }

                  capsulePipeline(Get(uri)).onSuccess {
                    case response: HttpResponse =>
                      import spray.json.lenses.JsonLenses._
                      import DefaultJsonProtocol._

                      val json = JsonParser(response.entity.asString(HttpCharsets.`UTF-8`))

                      val organisations = 'parties / optionalField("organisation") / arrayOrSingletonAsArray
                      val persons = 'parties / optionalField("person") / arrayOrSingletonAsArray
                      val firstPhoneNumber = 'contacts / 'phone / arrayOrSingletonAsArray / element(0) / 'phoneNumber

                      val organisationArrayIds = organisations / filter(('contacts / 'phone).is[JsValue](_ => true)) / 'id

                      val personArrayIds = persons / filter(('contacts / 'phone).is[JsValue](_ => true)) / 'id

                      ctx.complete(OK,
?>
<?xml version="1.0" encoding="utf-8" ?>
<CiscoIPPhoneDirectory>
	<Title><? $title ?></Title>
	<Prompt><? $title ?></Prompt>
<?
                            {
                              for (id <- json.extract[String](organisationArrayIds)) yield {
?>
		<DirectoryEntry>
			<Name>{ json.extract[String](organisations / filter('id.is[String](_ == id)) / 'name) }</Name>
			<Telephone>{ WHITESPACE.removeFrom(json.extract[String](organisations / filter('id.is[String](_ == id)) / firstPhoneNumber).head) }</Telephone>
		</DirectoryEntry>
<?
                              }
                            }
                            {
                              for (id <- json.extract[String](personArrayIds)) yield {
?>
		<DirectoryEntry>
			<Name>
<?
                                    { json.extract[String](persons / filter('id.is[String](_ == id)) / 'firstName) } { json.extract[String](persons / filter('id.is[String](_ == id)) / 'lastName) } {
                                      json.extract[String](persons / filter('id.is[String](_ == id)) / optionalField("organisationName")).headOption match {
                                        case None => ""
                                        case Some(on) => s" at $on"
                                      }
                                    }
?>
			</Name>
			<Telephone>{ WHITESPACE.removeFrom(json.extract[String](persons / filter('id.is[String](_ == id)) / firstPhoneNumber).head) }</Telephone>
		</DirectoryEntry>
<?
                              }
                            }
?>
</CiscoIPPhoneDirectory>
