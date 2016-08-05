# Recency Relevance
---
Relevance, when it comes to search sorting, consists of a complex algorithm that takes a number of factors into account. Some of these factors include text matching, click tracking, tags, etc.

One thing search_api_solr does not do particularly well is factor recency (publish or "created" date) into the equation. Here's an example of a block of code that will include the "create" date into the relevance algorithm:

```
/**
 * Implements hook_search_api_solr_query_alter.
 */
function splc_search_api_search_api_solr_query_alter(&$call_args, SearchApiQueryInterface $query) {
  // Boost value of recent content above older content.
  if ($call_args['query']) {
    $call_args['params']['dateboost'] = "recip(abs(ms(NOW,ds_created)),3.16e-11,1,.1)";
    $call_args['query'] = "{!boost b=\$dateboost v=" . $call_args['query'] . " defType=dismax}";
  }
}
```
_Please note:_ "created" needs to be added to the list of Fields in the Search API Solr "Fields" tab: `admin/config/search/search_api/index/default_node_index/fields`

There are a couple resources to help better understand use cases and what this chunk of code does:

* [Solr Wiki - FunctionQuery](https://wiki.apache.org/solr/FunctionQuery#recip)
* [Date-boosting Solr / Drupal search results](http://www.metaltoad.com/blog/date-boosting-solr-drupal-search-results)
* [Stronger boosting by date in Solr](http://stackoverflow.com/questions/22017616/stronger-boosting-by-date-in-solr/22213417#22213417)

Here are a couple helpful articles on Relevance:
* [What is Search Relevance?](http://opensourceconnections.com/blog/2014/06/10/what-is-search-relevancy/)
* [What Does "Relevant" Mean](http://www.searchtechnologies.com/meaning-of-relevancy)
