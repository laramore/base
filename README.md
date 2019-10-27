# Laramore Base

Commun base for all Laramore projects. Contains Element, Lock, Own, Observer definitions


# Installation
## Via composer

In your PHP project, run `composer require laramore/base`.


# Usage

This package is not meant to be used alone. It bundles different base classes, used in different Laramore packages.

## Elements

Elements are a sort of enumeration. They are managed with no specific order and can have different defined values.

### Element

An element has:
- a `name` (used by the manager to different it from others)
- different `values`.

`values` contains at least the key and a value for `"native"`.

An element is invokable. In this way, calling it, stringify it, returning a string value of `"native"`.

After being locked, this instance cannot be changed (`name` and `values`).

### ElementManager

Manage and regroup elements.
This class can only manage one type of elements, defined by `elementClass`.

`definitions` are all possible values commune to all elements. `"native"` is for example commune to all elements.


## Exceptions

Laramore use its own expections in order to allow the developer to detect the right exceptions.

### LaramoreException

Base exception for all Laramore exceptions.

It stores the instance creating this exception.

### LockException

This exception indicate the an exception occured during locking or that the instance is already locked if it needs to be unlocked to make a modification for example.

### OwnException

This exception indicate the an exception occured during owning or that the instance is already owned if it needs to be unowned to define a new owner for example.


## Interfaces

### IsALaramoreProvider

Indicate that the provider generate and lock a Laramore manager, fetchable by the developer by `Provider::getManager()`.

### IsLockable

Indicate that the class is lockable with the `lock` method.

### IsOwnable

Indicate that the class is ownable with the `own` method.

## Observers

Observers allow the developer to proxy Laravel base classes to handle calls, events and more.

### BaseHandler

A handler will group all order all observers for a specific class.

### BaseManager

A manager will group all handlers for a specific observation.

### BaseObserver

An observer will observe an action on a specific class, managed differently by the handler.


## Traits

### HasProperties

Add a property management, really usefull for Fields.

### IsLocked

Add multiple methods for lock management.

### IsOwned

Add multiple methods for own management.
