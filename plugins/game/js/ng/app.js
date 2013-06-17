// app.js - interim angular app
var IronbaneApp = angular.module('Ironbane', ['ngSanitize']);

IronbaneApp.directive('chatWindow', ['$log', function($log) {
    return {
        restrict: 'E',
        template: [
            '<div class="content">',
                '<ul class="messages">',
                    '<li ng-repeat="msg in messages">',
                        '<chat-message type="{{ msg.type }}" data="msg"></chat-message>',
                    '</li>',
                '</ul>',
            '</div>'
        ].join(''),
        link: function(scope, el, attrs) {
            scope.messages = [];

            var scroller = el.jScrollPane({
                animateScroll: true
            }).data('jsp');

            // hook into external (non-angular) sources
            el.bind('onMessage', function(e, data) {
                //$log.log('chat onMessage', e, data);

                scope.$apply(function() {
                    scope.messages.push(data);
                });

                scroller.reinitialise();
                scroller.scrollToBottom();
            });
        }
    };
}])
.directive('chatMessage', ['$log', '$compile', 'DEATH_MESSAGES', function($log, $compile, DEATH_MESSAGES) {
    // logic for all of the different types of messages that are supported
    var templates = {
        join: '<div><span class="name {{ data.user.rank }}">{{ data.user.name }}</span> has joined the game!</div>',
        died: '<div><span class="name {{ data.victim.rank }}">{{ data.victim.name }}</span> was {{ getDeathMsg() }} by <span class="name {{ data.killer.rank }}">{{ data.killer.name }}.</span>',
        leave: '<div><span class="name {{ data.user.rank }}">{{ data.user.name }}</span> has left the game.</div>',
        say: '<div><span class="name {{ data.user.rank }}"><{{ data.user.name }}></span> <span ng-bind-html="data.message"></span></div>',
        "announce": '<div class="message" ng-style="{color: data.message.color}" ng-bind-html="data.message.text"></div>',
        "announce:personal": '<div class="message" ng-style="{color: data.message.color}" ng-bind-html="data.message.text"></div>',
        "announce:mods": '<div class="message" ng-style="{color: data.message.color}" ng-bind-html="data.message.text"></div>',
        "default": '<div class="message">{{ data.message }}</div>'
    };

    function getTemplate(type) {
        //$log.log('getTemplate', type);
        if(!(type in templates)) {
            type = "default";
        }

        return templates[type];
    }

    return {
        restrict: 'E',
        scope: {
            type: '@',
            data: '='
        },
        template: '<div></div>',
        link: function(scope, el, attrs) {
            scope.getDeathMsg = function() {
                var random = Math.floor(Math.random() * DEATH_MESSAGES.length);

                return DEATH_MESSAGES[random];
            };

            el.html(getTemplate(scope.type));

            $compile(el.contents())(scope);
        }
    };
}])
.constant('DEATH_MESSAGES', "slaughtered butchered crushed defeated destroyed exterminated finished massacred mutilated slayed vanquished killed".split(" "));

// manually bootstrapping for now
angular.bootstrap('#chatBox', ['Ironbane']);