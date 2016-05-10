import activity from './activity';
import { combineReducers } from 'redux';
import invite from './invite';
import navigation from './navigation';
import progress from './progress';
import user from './user';

export default combineReducers({
    activity,
    invite,
    navigation,
    progress,
    user
});