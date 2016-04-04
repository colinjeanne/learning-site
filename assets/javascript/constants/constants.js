const pages = {
    ACTIVITIES:         'Activities',
    ADD_ACTIVITY:       'Add Activity',
    CHILD_PROGRESS:     'Child Progress',
    UNAUTHORIZED:       'Unauthorized',
    PROFILE:            'Profile'
};

const navigationPages = [
    pages.ACTIVITIES,
    pages.ADD_ACTIVITY,
    pages.CHILD_PROGRESS
];

export default {
    pages:                  pages,
    navigationPages:        navigationPages,
    
    // Navigation actions
    SHOW_PAGE:              'SHOW_PAGE',
    
    // User actions
    USER_SIGNIN:            'USER_SIGNIN',
    GET_ME:                 'GET_ME',
    GET_USER:               'GET_USER',
    UPDATE_ME:              'UPDATE_ME',
    
    // Family actions
    GET_MY_FAMILY:          'GET_MY_FAMILY',
    ADD_CHILD:              'ADD_CHILD',
    UPDATE_CHILD:           'UPDATE_CHILD',
    
    // Invite actions
    GET_MY_INVITATIONS:     'GET_MY_INVITATIONS',
    INVITE_FAMILY_MEMBER:   'INVITE_FAMILY_MEMBER',
    ACCEPT_INVITATION:      'ACCEPT_INVITATION'
};
