import Constants from '../constants/constants';
import { createAction } from 'redux-actions';
import {
    getRequestAction,
    postRequestAction,
    putRequestAction } from './requestHelpers';

export const getMyFamily =
    () => getRequestAction('/me/family', Constants.GET_MY_FAMILY);

export const addChild =
    name => postRequestAction(
        '/me/family/children',
        Constants.ADD_CHILD,
        {
            name
        });

export const updateChild =
    child => putRequestAction(
        child.links.self,
        Constants.UPDATE_CHILD,
        child);