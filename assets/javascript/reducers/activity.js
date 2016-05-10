import Constants from '../constants/constants';
import { handleActions } from 'redux-actions';

const initialState = {
    activities: [],
    dirtyIds: []
};

const mergeActivity = (activities, added) => {
    const existingIndex = activities.findIndex(
        activity => activity.links.self === added.links.self);
    if (existingIndex === -1) {
        return [
            ...activities,
            added
        ];
    }
    
    const starting = (existingIndex !== 0) ?
        activities.slice(0, existingIndex) :
        [];
    const ending = activities.slice(existingIndex + 1);
    return [
        ...starting,
        added,
        ...ending
    ];
};

const clearRemovedActivities = (existingActivities, newActivities) => {
    const newIds = newActivities.map(activity => activity.links.self);
    return existingActivities.filter(
        activity => newIds.indexOf(activity.links.self) !== -1);
};

const reducer = handleActions({
        [Constants.GET_MY_ACTIVITIES]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                const clearedActivities = clearRemovedActivities(
                    state.activities,
                    action.payload);
                const mergedActivities = action.payload.reduce(
                    (merged, activity) => mergeActivity(merged, activity),
                    clearedActivities);
                const dirtyIds = state.dirtyIds.filter(id =>
                    mergedActivities.find(activity =>
                        activity.links.self === id) !== -1);
                return Object.assign(
                    {},
                    state,
                    {
                        activities: mergedActivities,
                        dirtyIds
                    });
            },
            throw: state => state
        },
        
        [Constants.ADD_ACTIVITY]: {
            next: (state, action) => {
                if (!action.payload ||
                    action.error ||
                    (action.meta && action.meta.volatile)) {
                    return state;
                }
                
                const activities = mergeActivity(
                    state.activities,
                    action.payload);
                
                return Object.assign(
                    {},
                    state,
                    {
                        activities
                    });
            },
            throw: state => state
        },
        
        [Constants.GET_ACTIVITY]: {
            next: (state, action) => {
                if (!action.payload || action.error) {
                    return state;
                }
                
                const dirtyIds = state.dirtyIds.filter(id =>
                    id !== action.payload.links.self);
                
                const activities = mergeActivity(
                    state.activities,
                    action.payload);
                
                return Object.assign(
                    {},
                    state,
                    {
                        activities,
                        dirtyIds
                    });
            },
            throw: state => state
        },
        
        [Constants.UPDATE_ACTIVITY]: {
            next: (state, action) => {
                if (action.error) {
                    return state;
                }
                
                let dirtyIds = state.dirtyIds;
                if (action.meta && action.meta.volatile &&
                    (state.dirtyIds.indexOf(
                        action.payload.links.self) === -1)) {
                    dirtyIds = [...state.dirtyIds, action.payload.links.self];
                } else if (!action.meta || !action.meta.volatile) {
                    dirtyIds = state.dirtyIds.filter(id =>
                        id !== action.payload.links.self);
                }
                
                const activities = mergeActivity(
                    state.activities,
                    action.payload);
                
                return Object.assign(
                    {},
                    state,
                    {
                        activities,
                        dirtyIds
                    });
            },
            throw: state => state
        }
    },
    initialState);

export default reducer;