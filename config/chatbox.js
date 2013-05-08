
var latestChatLineId = localStorage.latestChatLineId ?
    localStorage.latestChatLineId : 0;
var waitingForReply = false;

var ChatTypes = {
    Chat:0,
    Announcement:1
};


var makeMemberLink = function(name) {
    return '<a href="user.php?n='+name+'">'+
        name+'</a>';
};

var checkForNewChatBoxMessages = function() {

    if ( waitingForReply ) return;

    waitingForReply = true;

    $.get("index.php?plugin=chat&action=getlines&last="+
        latestChatLineId, function(data) {

            waitingForReply = false;

            var stuffToAdd = "";

            for (var i = 0; i < data.lines.length; i++) {
                var messageData = data.lines[i];

                stuffToAdd += makeMemberLink(messageData.authorName)+
                    ': '+messageData.line+'<br>';

                latestChatLineId = messageData.id;
                localStorage.latestChatLineId = latestChatLineId;
            }

            // $("#chatBoxContent").append(stuffToAdd);


            if ( stuffToAdd ) {
                chatContentScroller.getContentPane().append(stuffToAdd);
                chatContentScroller.reinitialise();
                chatContentScroller.scrollToBottom();
            }

            localStorage.chatContentsBuffer = chatContentScroller.getContentPane().html();

            stuffToAdd = "";

            for (var i = 0; i < data.chatters.length; i++) {
                var chatterData = data.chatters[i];

                stuffToAdd += makeMemberLink(chatterData.name)+'<br>';
            }


            chattersScroller.getContentPane().html(stuffToAdd);
            chattersScroller.reinitialise();

            localStorage.chattersBuffer = stuffToAdd;

    }, "json");


};

// Variables
var hasChatFocus = false;
var lastUsedChat = new Array();
var lastUsedChatCounter = 0;
var lastUsedChatSelectCounter = 0;

var chatContentScroller = null;
var chattersScroller = null;

// Client input
var msg = '';
$(function() {

    chatContentScroller = $('#chatBoxContent').jScrollPane({
      animateScroll: true
    }).data('jsp');

    // $('#chatBoxWelcome').jScrollPane();
    chattersScroller = $('#chatBoxChatters').jScrollPane({}).data('jsp');

    if ( localStorage.chatContentsBuffer ) {
        chatContentScroller.getContentPane().append(localStorage.chatContentsBuffer);
        chatContentScroller.reinitialise();
        chatContentScroller.scrollToBottom(false);
    }

    if ( localStorage.chattersBuffer ) {
      chattersScroller.getContentPane().html(localStorage.chattersBuffer);
    }

    $('#chatInput').attr('value', msg);

    $('#chatInput').focus(function(){

      if ( $('#chatInput').attr('value') == msg ) {
        $('#chatInput').attr('value', '');
      }
      hasChatFocus = true;
    });
    $('#chatInput').blur(function(){

      if ( $('#chatInput').attr('value') == '' ) {
        $('#chatInput').attr('value', msg);
      }
      hasChatFocus = false;
    });
    $('#chatInput').keypress(function(e)
    {

      code= (e.keyCode ? e.keyCode : e.which);
      if (code == 13) {
        var clientmsg = $('#chatInput').val();
        lastUsedChat[lastUsedChatCounter++] = clientmsg;
        lastUsedChatSelectCounter = lastUsedChatCounter;

        $.post("index.php?plugin=chat", {
            text: clientmsg
        });

        $('#chatInput').attr('value', '');
      }
    });
    $('#chatInput').keydown(function(e)
    {

      code= (e.keyCode ? e.keyCode : e.which);
      if ( code == 45 ) {
        if ( player.target_unit ) {
          $('#chatInput').attr('value',
            $('#chatInput').attr('value') +
            ' '+player.target_unit.id+' ');
        }
      }
      if ( code == 38 ) {
        if ( lastUsedChatSelectCounter > 0 ) lastUsedChatSelectCounter--;
        $('#chatInput').attr('value',
          lastUsedChat[lastUsedChatSelectCounter]);
      }
      if ( code == 40 ) {
        if ( lastUsedChatSelectCounter < lastUsedChat.length - 1 )
          lastUsedChatSelectCounter++;

        $('#chatInput').attr('value',
          lastUsedChat[lastUsedChatSelectCounter]);
      }
    });



});


setInterval(checkForNewChatBoxMessages, 1000);
