@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h2 class="text-center">Logged as @auth() {{ \Auth::user()->name }} #{{ \Auth::user()->id }} @endauth</h2>
    </div>

    <div class="col-12 mb-3" temp-log>

    </div>

    <hr>
    <h6 class="my-3">Messages from xyz</h6>
    <div class="col-12">
        <ul class="list-group" message-list>
            <li class="mb-3 list-group-item">
                <div class="row">
                    <div class="col-8">Fixed</div>

                    <div class="col-3 px-4 text-end">
                        <span class="small text-muted">John</span>
                        |
                        <span class="small text-muted">2022-01-01 14:05</span>
                    </div>
                </div>
            </li>
        </ul>

        <hr>

        <div class="row">
            <div class="col-12 mb-3">
                <input
                    name="message-to"
                    id="message-to"
                    placeholder="message-to"
                    type="number"
                    minlength="1"
                    class="form-control" message-to="" />
            </div>

            <div class="col-12 mb-3">
                <textarea
                    name="message-content"
                    id="message-content"
                    placeholder="message-content"
                    cols="30" rows="10"
                    class="form-control" message-content=""></textarea>
            </div>

            <div class="col-12 mb-3">
                <button type="button" message-action="send" class="btn btn-success">Send</button>
            </div>
        </div>
    </div>
</div>

<script>
    const API_BASE_URL = "{{ url('/api') }}";
    const TOKEN = "{{ $token }}";

    function getCrsfToken() {
        var crsfMeta = document.querySelector('meta[name="csrf-token"]');

        if (!crsfMeta)
        {
            console.log('out: ', "{{ __FILE__.':'.__LINE__ }}");
            return false;
        }

        return crsfMeta.getAttribute('content')
    }

    async function getMe() {
        let rawResponse = await fetch('http://0.0.0.0:8000/api/me', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${TOKEN}`
            }
        })

        const content = await rawResponse.json();
        return content;
    }

    async function getMessagesFrom() {
        let rawResponse = await fetch('http://0.0.0.0:8000/api/messages', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${TOKEN}`
            }
        })

        const content = await rawResponse.json();
        return content;
    }

    function sendMessage() {
        var messageToElement = document.querySelector('input[message-to]')
        var messageContentElement = document.querySelector('textarea[message-content]')

        if (
            (!messageToElement || messageToElement.value < 1)
            ||
            (!messageContentElement || messageContentElement.value < 1)
        )
        {
            return null;
        }

        var sendTo          = messageToElement.value;
        var messageContent  = messageContentElement.value;


        var data = {
            '_token': '{{ csrf_token() }}',
            'to': sendTo,
            'message': messageContent
        };

    }

    function resetMessageContainer() {
        let container = document.querySelector('ul[message-list]');

        if (!container)
        {
            return null;
        }

        container.innerHTML = '';
    }

    function putMessage(messageData) {
        let container = document.querySelector('ul[message-list]');

        if (!container)
        {
            return null;
        }

        let listItem = document.createElement('li');
        listItem.classList.add('mb-3', 'list-group-item');

        let listItemContent = `
            <div class="row">
                <div class="col-8">#MESSAGE#</div>

                <div class="col-3 px-4 text-end">
                    <span class="small text-muted">#FROM#</span>
                    |
                    <span class="small text-muted">#DATE#</span>
                </div>
            </div>
        `;

        listItemContent = listItemContent.replace('#MESSAGE#', messageData.message);
        listItemContent = listItemContent.replace('#FROM#', messageData.from);
        listItemContent = listItemContent.replace('#DATE#', messageData.created_at);

        listItem.innerHTML = listItemContent;

        container.appendChild(listItem);
    }

    function loadMessages (reset = false) {
        getMessagesFrom().then(
            response => {
                var messages = response.data.data ? response.data.data : [];

                if (messages.lenght <= 0)
                {
                    return null;
                }

                if(reset) {
                    resetMessageContainer();
                }

                messages.forEach(
                    message => {
                        putMessage(message);
                    }
                );
            }
        )
    }

    //document.querySelector('[message-action="send"]')

    function jsonParseOrSelf(value) {
        try {
            return JSON.parse(value);
        } catch {
            return value;
        }
    }

    function putTempMessage(content) {
        let contentToPut = JSON.stringify(content, null, 4);

        let tempMessageContainer = document.querySelector('[temp-log]');

        if (!tempMessageContainer)
        {
            return null;
        }

        tempMessageContainer.innerHTML = contentToPut;
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadMessages(true);

        getMe().then((userData) => {
            let loggedUser = userData;

            if (!(!!loggedUser && !!window.Echo))
            {
                console.table({
                    loggedUser: !!loggedUser,
                    window_echo: !!window.Echo
                });
                return null;
            }

            if(loggedUser && window.Echo) {
                window.Echo.private('user.' + loggedUser.id)
                    .listen('.MessageSended', (e) => {

                        if(e.message_data) {
                            putTempMessage(e);
                            console.table(e);
                            putMessage(e.message_data);
                        }
                    });
            }
        });
    });
</script>
@endsection
