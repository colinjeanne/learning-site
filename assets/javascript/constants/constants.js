const pages = {
    ACTIVITIES:         'Activities',
    ADD_ACTIVITY:       'Add Activity',
    CHILD_PROGRESS:     'Child Progress',
    EDIT_ACTIVITY:      'Edit Activity',
    PROFILE:            'Profile',
    UNAUTHENTICATED:    'Unauthenticated',
    VIEW_ACTIVITY:      'View Activity'
};

const navigationPages = {
    [pages.ACTIVITIES]: {
        narrow: '\uD83C\uDFA8',
        wide: 'Activities'
    },

    [pages.CHILD_PROGRESS]: {
        narrow: '\uD83D\uDEBC',
        wide: 'Child Progress'
    }
};

export default {
    pages:                  pages,
    navigationPages:        navigationPages,

    // Navigation actions
    ACTIVITY_FILTERED:      'ACTIVITY_FILTERED',
    SELECT_ACTIVITY:        'SELECT_ACTIVITY',
    SHOW_PAGE:              'SHOW_PAGE',

    // Progress page actions
    SKILL_FILTERED:         'SKILL_FILTERED',

    // Activity actions
    GET_MY_ACTIVITIES:      'GET_MY_ACTIVITIES',
    ADD_ACTIVITY:           'ADD_ACTIVITY',
    GET_ACTIVITY:           'GET_ACTIVITY',
    UPDATE_ACTIVITY:        'UPDATE_ACTIVITY',

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
