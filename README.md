[![Build Status](https://travis-ci.org/wizbii/pipeline.svg?branch=master)](https://travis-ci.org/wizbii/pipeline)

# Alpha Relase

# Project Goal
Wizbii's Pipeline is a framework used to deal with backend pipelines. By "pipeline", we mean a list of actions that need to be performed following an event.
Pipeline is a designed to be represented as a graph. It provides a JSON API to get the graph details. In a few weeks, we will release a first version of a webapp using this API to help displaying this API. It aims is to be used as an architecture document.

We use Pipeline at Wizbii in conjunction with our CQRS approach to deal with our data.

# Main Architecture

The architecture is broken into several actors :
   * *Event* : an Event is thrown to or catched from a Messaging system like RabbitMQ, HornetQ, ... I contains only a few information since it is send over the network. For simplicity and consistency, Events are not persisted like in most Event Sourcing solutions
   * *Action* : an Action is the internal representation of an Event. It is send by the Dispatcher the registered stores. Since an Action is only living in memory, it can contain more information than a simple Event. Actions are created after an Event ash been catched.
   * *Store* : a Store is accountable for managing a Projection. Each store is registered to run after an Action has been created or after another Store has finished its task. In a Symfony world, a Store is implemented by a service

# Installation

# Configuration

To configure a Pipeline, you can define a simple YML file included in your mail config.yml file See below for the reference of this file :

    # Default configuration for extension with alias: "wizbii_pipeline"
    wizbii_pipeline:
        actions:

            # Prototype
            name:
                triggered_by_events:  []
        stores:

            # Prototype
            name:
                service:              ~
                triggered_by_actions:  []
                triggered_by_stores:  []
                triggered_events:     []

A very simple example of such file in a social network context :

    wizbii_pipeline:
        actions:
            profile_updated: ~
            profile_anniversary: ~
            profile_new_friends:
                triggered_by_events: [profile_new_connection, profile_friends_new_connection, profile_new_school, profile_school_new_student]
            profile_update_thanx: ~
    
        stores:
            # This store updates the projection containing profile network : friends, friends of friends and school friends
            profile_network:
                service: wizbii.pipeline.stores.profile.network
                triggered_by_actions: [profile_new_friends]
    
            # This store updates the projection containing profile thanxers
            profile_thanx:
                service: wizbii.pipeline.stores.profile.network
                triggered_by_actions: [profile_update_thanx]
    
            # This store updates the projection containing profile identity card : first_name, last_name, title, age, network
            # and thanx counters
            profile_identity_card:
                service: wizbii.pipeline.stores.profile.identity_card
                triggered_by_actions: [profile_updated, profile_anniversary]
                triggered_by_stores: [profile_network, profile_thanx]
    
            # This store updates the projection containing profile proxy
            profile_proxy:
                service: wizbii.pipeline.stores.profile.profile_proxy
                triggered_by_actions: [profile_updated, profile_anniversary]
                triggered_by_stores: [profile_network, profile_thanx]
    
            # This store updates the projection containing profile stats
            profile_stats:
                service: wizbii.pipeline.stores.profile.profile_stats
                triggered_by_actions: [profile_updated, profile_anniversary]
                triggered_by_stores: [profile_network, profile_thanx]
    
            # This store updates the projection containing ESProfile. Indexation in ElasticSearch is done asynchronously
            profile_search:
                service: wizbii.pipeline.stores.profile.profile_search
                triggered_by_actions: [profile_updated, profile_anniversary]
                triggered_by_stores: [profile_network, profile_thanx]
                triggered_events: [esprofile_updated]
