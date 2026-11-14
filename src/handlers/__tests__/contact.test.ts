import type { Request, Response } from "express";
import { beforeEach, expect, mock, test } from "bun:test";
import { handleContact } from "../contact";
import { send } from "../../email/email";

const mockEmailSend = mock();
const mockResSend = mock();
const mockStatus = mock();

let mockRes: Response;
let mockReq: Request;

beforeEach(() => {
  mock.module("../../email/email", () => ({
    send: mockEmailSend,
  }));

  mock.restore();
  mockEmailSend.mockClear();
  mockStatus.mockClear();
  mockResSend.mockClear();

  mockStatus.mockImplementation(() => {
    return { send: mockResSend };
  });

  mockRes = {
    status: mockStatus,
    send: mockResSend,
  } as unknown as Response;

  mockReq = {
    body: {
      firstname: "User",
      lastname: "Person",
      email: "user@example.com",
      message: "Message here",
    },
  } as unknown as Request;
});

test("should send an email and return 200 Success when all inputs are valid", async () => {
  mockEmailSend.mockResolvedValue(true);

  await handleContact(mockReq, mockRes);

  expect(send).toHaveBeenCalledTimes(1);
  expect(send).toHaveBeenCalledWith({
    from: "user@example.com",
    to: "recipient@email.com",
    subject: "New contact request",
    message: "Message here",
    res: mockRes,
  });
  expect(mockResSend).toHaveBeenCalledWith({ msg: "Success" });
});

test("should return 500 Whoops when email sending fails", async () => {
  mockEmailSend.mockResolvedValue(false);

  await handleContact(mockReq, mockRes);

  expect(mockEmailSend).toHaveBeenCalledTimes(1);
  expect(mockStatus).toHaveBeenCalledWith(500);
  expect(mockResSend).toHaveBeenCalledWith({ msg: "Whoops" });
});

test("should return 400 when all required fields are missing", async () => {
  mockReq.body = {};

  await handleContact(mockReq, mockRes);

  expect(mockStatus).toHaveBeenCalledWith(400);
  expect(mockResSend).toHaveBeenCalledWith({
    error: "Required values firstname, lastname, from, message missing ",
  });
  expect(send).not.toHaveBeenCalled();
});

test("should return 400 listing only the specific missing fields", async () => {
  mockReq.body = {
    firstname: "Jane",
    email: "jane.doe@example.com",
  };

  await handleContact(mockReq, mockRes);

  expect(mockStatus).toHaveBeenCalledWith(400);
  expect(mockResSend).toHaveBeenCalledWith({
    error: "Required values lastname, message missing ",
  });
  expect(send).not.toHaveBeenCalled();
});
