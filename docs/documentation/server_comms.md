# server communications:

* servers can remove imported elements, tokens, ns without notice to others, it is their own business
* elsewhere do not ask for elements
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
 (events here NEW_ELSEWHERE_EVENT, events there RUN_EVENT_ELSEWHERE)

 when we destroy elements, and its given to another server, we can ask them to destroy it
 (there  ELSEWHERE_DESTROYED_ELEMENT)

 when we suspend a type, we ask the types registered elsewhere to do something
 (there ELSEWHERE_SUSPENDED_TYPE)


  if the server is not registered, then register and then exchange keys (
  * servers are users, so the make users and server namespaces for each other, log the other in, and exchange the tokens.
  * when needing to update the tokens use regular user api


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
  CMD_ELSEWHERE_CHANGE_STATUS
  CMD_ELSEWHERE_UNREGISTER

# CMD_ELSEWHERE_DO_REGISTRATION
  The server is registered as its own user, and namespace, as well as a server,the server is sent back a login token it uses to send stuff here .
if the server name is already taken in the default user table, then a random username chosen, but the default ns for this is one associated with the server id for it
so the server ns name is the same as the server's


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

## API the elsewhere calls here

This is protected to be in the server ns or its admin group

* import_element -- when given one or more elements
* nudge_element -- the caller already has the element (or list of elements), and wants those given to another server
* import_set    -- when given one or more sets
* export_type  -- must be given an element first, when this called the type (or list of types) is exported
* export_namespace -- must be given an element with owner or type with owner first, can be list of ns in one call
* import_event -- when calling the event from outside, can be multiple events
* update_type_status -- update the status of the type lifestyle
* update_element_status -- let them know if destroyed
  

    //event listeners on the server ns here can do after event listeners on all server events for them

# Notes

note: there is nothing stopping a server from creating invalid elements or doing illegal value changes; or passing on the element in invalid ways
this does not matter unless somehow this element or values is needed on the original server, either directly or through rule verification when something references it.

Element data can be encrypted to make sure data is not changed on other server
note: can verify with the public key on rule that listens to incoming elsewhere and gets the public key attribute from the namespace token

element owners cannot control servers migrating the data, only the type can, but the type can have rules to allow or disallow based on element ns.

any element creation from types exported to elsewhere must call the originating server for this new element.
Then, the element is created on the original server and exported to the elswhere, otherwise this is an invalid element
(when the element is sent back or events called on it, the uuid is missing so invalid)

if one does not want an attribute readable, then keep its access out of the elswhere
