import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    activityFilter: undefined,
    page: Constants.pages.UNAUTHENTICATED,
    selectedActivityId: undefined
};

const reducer = handleActions({
        [Constants.ACTIVITY_FILTERED]: (state, action) =>
            Object.assign(
                {},
                state,
                { activityFilter: action.payload }),

        [Constants.ADD_ACTIVITY]: {
            next: (state, action) => {
                if (!action.payload ||
                    action.error ||
                    (action.meta && action.meta.volatile)) {
                    return state;
                }
                
                return Object.assign(
                    {},
                    state,
                    {
                        page: Constants.pages.ACTIVITIES,
                        selectedActivityId: undefined
                    });
            },
            throw: state => state
        },
        
        [Constants.SELECT_ACTIVITY]: (state, action) =>
            Object.assign(
                {},
                state,
                {
                    page: Constants.pages.VIEW_ACTIVITY,
                    selectedActivityId: action.payload
                }),

        [Constants.SHOW_PAGE]: (state, action) => {
            const isShowingActivity =
                (action.payload == Constants.pages.EDIT_ACTIVITY) ||
                (action.payload == Constants.pages.VIEW_ACTIVITY);
            const selectedActivityId = isShowingActivity ?
                state.selectedActivityId :
                undefined;
            
            return Object.assign(
                {},
                state,
                {
                    activityFilter: undefined,
                    page: action.payload,
                    selectedActivityId
                });
        },
        
        [Constants.USER_SIGNIN]: state =>
            Object.assign(
                {},
                state,
                {
                    activityFilter: undefined,
                    page: Constants.pages.ACTIVITIES,
                    selectedActivityId: undefined
                })
    },
    initialState);

export default reducer;