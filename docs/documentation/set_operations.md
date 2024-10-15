A,B,C is set
P Q is path that selects attr or types, or ns (must be actual ns not their elements or types for filtering ownership of element or type)
M mutual
e element

* combine
  (p)A op (q)B => C
  OR, XOR, AND
  to remove also can chain to filter of some other thing that gets elements

* pop
  (p)A => B(+e) + A(-e)
  removes last to be added, with p doing different ordering, the p min and max control how many elements should be processed. B can be null.
  P can be null, only provide min and or max, or select the elements in which order will be popped

* shift
  removes from first to be added, the p min and max control how many elements should be processed.B can be null.
  P can be null, only provide min and or max, or select the elements in which order will be shifted
  (p)A -> B(+e) + A(-e)

* push adds e to the last of the set,the p min and max control how many elements should be processed.
  (p) -> A(+e)

* unshift adds e to the first of the set, the p min and max control how many elements should be processed.
  (p) -> A(-e)

* mutual A (B)
  (p) => M
  finds all the sets that share elements, (with path restricting the set/element chosen). M is a set without events
