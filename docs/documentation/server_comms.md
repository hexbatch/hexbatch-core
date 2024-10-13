# server communications:

* servers can remove imported elements, tokens, ns without notice to others, it is their own business
* elsewhere do not ask for elements
* server_namespace_tokens only applies to ns for other servers,  the elsewhere never runs stuff in the ns name, rules stay on home server, rename user_server_token
* when a server recieves a type, it can put its own event hooks that run at the same time the events are sent to the elewhere.



 ## server flow
  servers introduce themselves by giving an element ( events here PUSH_ELEMENT_ELSEWHERE, events there NEW_ELSEWHERE_ELEMENT)
   CMD_ELSEWHERE_GIVE_ELEMENT

  this can make the elsewhere then ask for the element type and ns

  * CMD_ELSEWHERE_GIVE_TYPE ( events here PUSH_TYPE_ELSEWHERE, events there NEW_ELSEWHERE_TYPE)
  * CMD_ELSEWHERE_GIVE_NS ( events here PUSH_NS_ELSEWHERE, events there NEW_ELSEWHERE_NS)
  * later can give sets
  CMD_ELSEWHERE_GIVE_SET ( events here PUSH_SET_ELSEWHERE, events there NEW_ELSEWHERE_SET)


 sometimes the elements we give, are not ours, they were given to us
   (here SHARING_ELSEWHERE_ELEMENT ,there PUSH_TO_NEXT_SERVER) then starts the giving of the element again and its flow

 elements, on the elsewhere, can have events called that has to be resolved here
   events here NEW_ELSEWHERE_EVENT, events there RUN_EVENT_ELSEWHERE)

 when we destroy elements, and its given to anther server, we can ask them to destroy it
 (there  ELSEWHERE_DESTROYED_ELEMENT)

 when we suspend a type, we ask the types registered elsewhere to do something
 (there ELSEWHERE_SUSPENDED_TYPE)


  if the server is not registered, then register and then exchange keys (
  * CMD_ELSEWHERE_DO_REGISTRATION (here SERVER_REGISTERED, there SERVER_REGISTERED)
  * CMD_ELSEWHERE_REGENERATE_KEY (here SERVER_REGENERATE_SERVER_KEY ,there NEW_ELSEWHERE_KEY


change status as needed
 CMD_ELSEWHERE_CHANGE_STATUS (here SERVER_STATUS_ALLOWED, SERVER_STATUS_DENIED,SERVER_STATUS_PAUSED) other server clueless

 unregistering the server requires a polite call to it
 CMD_ELSEWHERE_UNREGISTER
 this unregisters the server (here SERVER_UNREGISTERED, there SERVER_UNREGISTERED)


 ## server commands
  CMD_ELSEWHERE_GIVE_ELEMENT
  CMD_ELSEWHERE_GIVE_TYPE
  CMD_ELSEWHERE_GIVE_NS
  CMD_ELSEWHERE_GIVE_SET
  CMD_ELSEWHERE_DO_REGISTRATION
  CMD_ELSEWHERE_REGENERATE_KEY
  CMD_ELSEWHERE_CHANGE_STATUS
  CMD_ELSEWHERE_UNREGISTER


## server events

* PUSH_ELEMENT_ELSEWHERE 
* PUSH_SET_ELSEWHERE 
* PUSH_TYPE_ELSEWHERE 
* PUSH_NS_ELSEWHERE 
* RUN_EVENT_ELSEWHERE 

* NEW_ELSEWHERE_ELEMENT 
* NEW_ELSEWHERE_SET 
* NEW_ELSEWHERE_EVENT
* NEW_ELSEWHERE_TYPE
* NEW_ELSEWHERE_NS
* NEW_ELSEWHERE_KEY 


* ELSEWHERE_SUSPENDED_TYPE 
* ELSEWHERE_DESTROYED_ELEMENT 
* SHARING_ELSEWHERE_ELEMENT 
* PUSH_TO_NEXT_SERVER 


* SERVER_REGISTERED 
* SERVER_UNREGISTERED 
* SERVER_STATUS_ALLOWED 
* SERVER_STATUS_DENIED 
* SERVER_STATUS_PAUSED 
* SERVER_REGENERATE_SERVER_KEY 
  

    //event listeners on the server ns here can do after event listeners on all server events for them
