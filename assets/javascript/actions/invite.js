import Constants from '../constants/constants';
import { createAction } from 'redux-actions';
import { getRequestAction, postRequestAction } from './requestHelpers';

export const getMyInvitations =
    () => getRequestAction('/me/invitations', Constants.GET_MY_INVITATIONS);

export const inviteFamilyMember =
    id => postRequestAction(
        '/me/invitations',
        Constants.INVITE_FAMILY_MEMBER,
        id);

export const acceptInvitation =
    id => postRequestAction(id, Constants.ACCEPT_INVITATION);