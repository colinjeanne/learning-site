import Constants from '../constants/constants';
import { createAction } from 'redux-actions';
import { getRequestAction, putRequestAction } from './requestHelpers';
import { getMyFamily } from './family';

export const signInUser = createAction(Constants.USER_SIGNIN);

export const getMe = () => getRequestAction('/me', Constants.GET_ME);

export const getUser = id => getRequestAction(id, Constants.GET_USER);

export const updateMe = (id, name) =>
    putRequestAction(
        id,
        Constants.UPDATE_ME,
        {
            name
        });