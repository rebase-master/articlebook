appControllers.controller('ArticleController', ['$scope', '$rootScope', '$http', '$routeParams', '$location',  function($scope, $rootScope, $http, $routeParams, $location) {

    "use strict";

    var dataUrl,
        start = 0,
        offset = 20;

    $rootScope.baseUrl  =    baseUrl;

    console.log("Timeago");
    //$scope.isLiked = function (qid) {
    //    return $.inArray(parseInt(qid), likes) >= 0;
    //};
    //$scope.isFav = function (qid) {
    //    return $.inArray(parseInt(qid), favs) >= 0;
    //};
    //
    //$rootScope.bs = function (sbi) {
    //    dataUrl = parseInt($rootScope.qfeed) == -1? 'q/qs': 'q/ax/follow';
    //    loadData(dataUrl);
    //}

    $rootScope.uCat = $rootScope.loaded = false;
    $scope.loginClass =  loginClass;
    $scope.uid        =  uid;
    $scope.hasMore    =  0;

    var loader = $('.sqc').find('.loading');

    loadData(dataUrl);

    function loadData(){
        NProgress.start();
        Quotes.fetchQuotes(baseUrl+dataUrl, {'uid': uid, start: 0, dev: screen.width+'X'+screen.height})
            .then(function (response) {
                $scope.quoteEmpty = response.quotes.length == 0;
                $scope.quotes   = response.quotes;
                $scope.dif = response.dif;
                $scope.hasMore = response.hasmore;
                $rootScope.loaded = true;
                init();
                start = 0;
                NProgress.done();
            }, function (data) {
                NProgress.done();
            });
    }

    //Load more quotes when load more button is clicked
    $scope.loadMore = function () {
        NProgress.start();
        start = start + offset;
        Quotes.fetchQuotes(baseUrl+dataUrl, {'uid': uid, 'start': start, 'offset': offset, dev: screen.width+'X'+screen.height})
            .then(function (response) {
                $scope.quotes = $scope.quotes.concat(response.quotes);
                $scope.dif = response.dif;
                $scope.hasMore = response.hasmore;
                $rootScope.loaded = true;
                init();
                NProgress.done();
            }, function (data) {
                console.log("Error loading data!");
                NProgress.done();
            });
    };

}]);
