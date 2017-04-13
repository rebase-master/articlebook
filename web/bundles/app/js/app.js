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
appControllers.controller('ArticleController', ['$scope', '$rootScope', '$http', '$routeParams', '$location', '$compile', function($scope, $rootScope, $http, $routeParams, $location, $compile) {

    "use strict";

    $scope.articleLink = '';
    $scope.articleComment ='';
    $scope.atags = [];
    $scope.articles = null;

    loadArticles();

    $scope.like = function (event, articleId, mode) {

        var url, likeText,
            target = event.currentTarget,
            lctrCont = $(target).closest('.interaction').find('.lctr'),
            lctr     = parseInt(lctrCont.text())

            ;
        console.log(mode);
        mode= parseInt(mode);

        if(mode == -1){
            url = baseUrl+'articles/'+articleId+'/unlike';
            likeText = 'Like';
        }else{
            url = baseUrl+'articles/'+articleId+'/like';
            likeText = 'Unlike';
        }

        $http({
            method: 'POST',
            url: url,
            cache: false
        }).success(function(response){
            $(target).parent().empty().append(
                $compile(
                    "<button ng-click='like($event,"+articleId+","+(mode*-1)+")' class='btn btn-xs btn-primary'><span class='glyphicon glyphicon-thumbs-up'></span> <span class='likeText'>"+likeText+"</span></button>"
                )($scope)
            );
            //$scope.apply();
            //$(target).parent().html('<button ng-click="like($event, '+articleId+', '+(mode*-1)+')" class="btn btn-xs btn-primary"><span class="glyphicon glyphicon-thumbs-up"></span> <span class="likeText">'+likeText+'</span></button>');
        }).error(function (response) {
           alert("Something went wrong.");
        });
    };

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

    $scope.addComment = function (event, articleId) {

        var url = baseUrl+'articles/'+articleId+'/comments/add',
            key = event.keyCode || event.which,
            target = event.target;

        if(key == 13){
            var comment = $(target).val().trim();
            if(comment != ''){
                //$(target).parent().next('.comments').prepend('my comment');
                saveComment(url, comment, articleId, $(target));
            }
        }
    };

    function saveComment(url, comment, articleId, ele) {
        NProgress.start();
        $http({
            method: 'POST',
            url: url,
            cache: false,
            params: {comment: JSON.stringify(comment)}
        }).success(function(response) {
            NProgress.done();
            ele.val('');
            if(response.code == 1) {
                ele.parent().next('.comments').prepend(
                    $compile(
                        "<div class='sqr'>" +
                        "<input type='hidden' class='cid' name='cid' value='" + response.comment.id + "' />" +
                        "<div class='comment'>" +
                        "<a ng-href='" + response.comment.userProfileLink + "'>" + response.comment.username +
                        "</a>" +
                        "<div class='text'>" + response.comment.comment + "</div>" +
                        "<span ng-if='" + uid + " == " + response.comment.userId + "' ng-click='removeComment($event, " + articleId + ", " + response.comment.id + ")' class='_rct remove pull-right glyphicon glyphicon-remove'></span>" +
                        "</div>" +
                        "</div>"
                    )($scope)
                );

            }
        }).error(function (response) {
            NProgress.done();
            alert("something went wrong.");
        });
    }

    $scope.removeComment = function (event, articleId, commentId) {

        var url = baseUrl+'articles/'+articleId+'/comments/delete/'+commentId;
        $http({
            method: 'DELETE',
            url: url,
            cache: false,
        }).success(function(response) {
            NProgress.done();

        }).error(function (response) {
            NProgress.done();
        });
    }


}]);

