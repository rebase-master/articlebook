/* global angular, $, baseUrl */

var ahApp = angular
    .module('ahApp', ['ngRoute','appControllers','ngAnimate','ui.bootstrap', 'yaru22.angular-timeago'])
    .constant('BASE_URL', baseUrl)
    .config(['$httpProvider', '$interpolateProvider',
        function($httpProvider, $interpolateProvider) {
            $interpolateProvider.startSymbol('{[');
            $interpolateProvider.endSymbol(']}');
            $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
        }])
    ;

var appControllers = angular.module('appControllers', []);
appControllers.controller('ArticleController', ['$scope', '$rootScope', '$http', '$routeParams', '$location',  function($scope, $rootScope, $http, $routeParams, $location) {

    "use strict";

    $scope.articleLink = '';
    $scope.atags = [];
    $scope.articles = null;

    loadArticles();

    $scope.addTags = function (event) {
        var key = event.keyCode || event.which,
            target = event.target;

        if(key == 13){
            $scope.atags.push({name: $(target).val()});
            $(target).val('');
        }
    }//addTag()

    $scope.removeTag = function (event) {
        var target = event.target,
            tag = $(target).prev('.tag').text().substr(1);
        $scope.atags = $scope.atags.filter(function( obj ) {
            return obj.name !== tag;
        });
    }//removeTag()

    function loadArticles(){

        var url = baseUrl+'articles/';
        NProgress.start();
        $http({
            method: 'GET',
            url: url,
            cache: true
        }).success(function(response) {
            console.log("articles: ");
            console.log(response.articles);
            $scope.articles = response.articles;
            NProgress.done();

        }).error(function (response) {
            NProgress.done();
        });


    }//loadArticles

    $scope.addArticle = function () {

        var url = baseUrl+'articles/new';
        if($scope.articleLink != '' && $scope.articleCategory != '')
            saveArticle(url);
    };

    function saveArticle(url) {
        NProgress.start();
        $http({
            method: 'POST',
            url: url,
            cache: false,
            params: {link: $scope.articleLink, tags: JSON.stringify($scope.atags), category: $scope.articleCategory}
        }).success(function(response) {
            NProgress.done();

        }).error(function (response) {
            NProgress.done();
        });
    }


}]);

