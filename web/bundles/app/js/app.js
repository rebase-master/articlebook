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

    $scope.addTags = function (event) {
        var key = event.keyCode || event.which,
            target = event.target;

        if(key == 13){
            console.log("value: "+$(target).val());
            $scope.atags.push({name: $(target).val()});
            $(target).val('');
        }
    }//addTag()

    $scope.removeTag = function (event) {
        var target = event.target,
            tag = $(target).prev('.tag').text().substr(1);
        console.log("deleting: ", tag);
        $scope.atags = $scope.atags.filter(function( obj ) {
            return obj.name !== tag;
        });
            //$scope.atags.splice($scope.atags.,1)
            //
            //$scope.atags.push({name: $(target).val()});
            //$(target).val('');
        //}
    }//removeTag()

    $scope.addArticle = function () {

        console.log("clicked");
        console.log($scope.articleLink);
        var url = baseUrl+'articles/new';
        if($scope.articleLink != '')
            saveArticle(url, $scope.articleLink);
    };

    function saveArticle(url, val) {
        NProgress.start();
        console.log(val);
        $http({
            method: 'POST',
            url: url,
            cache: false,
            params: {link: val}
        }).success(function(response) {
            NProgress.done();

        }).error(function (response) {
            NProgress.done();
        });

    }


}]);
